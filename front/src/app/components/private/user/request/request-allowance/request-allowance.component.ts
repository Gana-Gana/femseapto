import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { LoginService } from '../../../../../services/login.service';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { RequestAllowanceService } from '../../../../../services/request-allowance.service';
import { AllowanceTypeService } from '../../../../../services/allowance-type.service';
import { InfoRequestAllowanceComponent } from './info-request-allowance/info-request-allowance.component';


@Component({
  selector: 'app-request-allowance',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, InfoRequestAllowanceComponent],
  providers: [MessageService],
  templateUrl: './request-allowance.component.html',
  styleUrls: ['./request-allowance.component.css']
})
export class RequestAllowanceComponent implements OnInit {
  allowanceForm: FormGroup;
  allowanceTypes: any[] = [];
  selectedAllowanceType: any = null;

  displayMessageNatPerson: string = '';
  isAdditionalDisabled: boolean = false;

  pdfFiles: File[] = [];
  isSubmitting: boolean = false;

  documentRequirements: { [key: number]: string[] } = {
    1: ['Solicitud diligenciada', 'Registro Civil de Nacimiento (Copia auténtica)'],
    2: ['Fórmula médica', 'Factura con requisitos legales'],
    3: ['Solicitud escrita', 'Soportes según tipo de calamidad'],
    4: ['Documento médico del tratamiento', 'Facturas legales', 'Solicitud formal', 'Comprobante de pago'],
    5: ['Promesa de compraventa', 'Recibo de pago notarial (Máx. 30 días)'],
    6: ['Constancia de estudio o recibo de pago con sello', 'Presentación certificado de notas del periodo cursado']
  };

  additionalFiles: { label: string; file?: File | null }[] = [];


  onAdditionalFileChange(event: Event, index: number): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      const file = input.files[0];
      if (file.type === 'application/pdf') {
        this.additionalFiles[index].file = file;
      } else {
        this.messageService.add({
          severity: 'error',
          summary: 'Archivo no válido',
          detail: 'Solo se permiten archivos en formato PDF'
        });
        input.value = '';
      }
    }
  }
  constructor(
    private fb: FormBuilder,
    private allowanceRequestService: RequestAllowanceService,
    private loginService: LoginService,
    private messageService: MessageService,
    private router: Router,
    private allowanceTypeService: AllowanceTypeService,
    private naturalpersonService: NaturalpersonService
  ) {
    this.allowanceForm = this.fb.group({
      idTipoAuxilio: ['', Validators.required],
      descripcion: ['', Validators.required],
      adjuntosAuxilio: [null, Validators.required]
    });
  }

  ngOnInit(): void {
    this.validateUserRecords();

    this.allowanceTypeService.getActive().subscribe(
      data => {
        this.allowanceTypes = data;
      },
      error => {
        console.error('Error al obtener tipos de auxilios activos:', error);
      }
    );
  }

  validateUserRecords(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
      let allValid = true;

      this.naturalpersonService.validate(token.userId).subscribe(response => {
        if (!response) {
          this.displayMessageNatPerson = 'Por favor, registre la información general';
          this.messageService.add({ severity: 'warn', summary: 'Aviso', detail: this.displayMessageNatPerson });
          allValid = false;
        }
        this.checkValidationComplete(allValid);
      });
    }
  }

  checkValidationComplete(allValid: boolean): void {
    this.isAdditionalDisabled = !allValid;
  }

  onFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      const selectedFiles = Array.from(input.files);
      const validFiles = selectedFiles.filter(file => file.type === 'application/pdf');

      if (validFiles.length !== selectedFiles.length) {
        this.messageService.add({
          severity: 'error',
          summary: 'Archivos no válidos',
          detail: 'Algunos archivos no son PDF y fueron descartados'
        });
      }

      this.pdfFiles = validFiles;

      if (this.pdfFiles.length > 0) {
        this.allowanceForm.patchValue({ adjuntosAuxilio: this.pdfFiles });
        this.allowanceForm.get('adjuntosAuxilio')?.updateValueAndValidity();
      } else {
        this.messageService.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Debe subir al menos un archivo PDF válido'
        });
        input.value = '';
      }
    }
  }
  onSubmit(): void {

    if (!this.validateAllDocuments()) {
    return;
  }
    if (this.allowanceForm.valid) {
      this.isSubmitting = true;
      const token = this.loginService.getTokenClaims();

      if (token) {
        const userId = token.userId;
        const formData = new FormData();

        formData.append('idUsuario', userId);
        formData.append('idTipoAuxilio', this.allowanceForm.value.idTipoAuxilio);
        formData.append('descripcion', this.allowanceForm.value.descripcion);
        if (this.pdfFiles.length > 0) {
          this.pdfFiles.forEach(file => {
            formData.append('adjuntosAuxilio[]', file, file.name);
          });

          
          this.additionalFiles.forEach((doc) => {
            if (doc.file) {
              formData.append('adjuntosAuxilio[]', doc.file, doc.file.name);
            }
          });

        } else {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Debe subir al menos un archivo' });
          this.isSubmitting = false;
          return;
        }

        this.allowanceRequestService.create(formData).subscribe({
          next: () => {
            this.messageService.add({ severity: 'success', summary: 'Éxito', detail: 'Solicitud de auxilio creada correctamente' });
            setTimeout(() => {
              this.isSubmitting = false;
              this.router.navigate(['/auth/user']);
            }, 2000);
          },
          error: (err) => {
            this.isSubmitting = false;
            this.messageService.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear la solicitud de auxilio. Por favor, intente otra vez' });
          }
        });
      }
    }
  }


  onAllowanceTypeChange(): void {
    const selectedId = +this.allowanceForm.get('idTipoAuxilio')?.value;
    this.additionalFiles = [];

    if (this.documentRequirements[selectedId]) {
      this.additionalFiles = this.documentRequirements[selectedId].map(label => ({
        label,
        file: null
      }));
    }
  }

  validateAllDocuments(): boolean {
  if (this.additionalFiles.length > 0) {
    const missingDocs = this.additionalFiles.filter(doc => !doc.file);
    if (missingDocs.length > 0) {
      this.messageService.add({
        severity: 'error',
        summary: 'Documentos requeridos',
        detail: 'Todos los documentos adicionales son obligatorios.'
      });
      return false;
    }
  }
  return true;
}



}