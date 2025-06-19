import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, AbstractControl, ValidationErrors } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { CurrencyFormatPipe } from '../../../../pipes/currency-format.pipe';
import { LoginService } from '../../../../../services/login.service';
import { MessageService } from 'primeng/api';
import { Router } from '@angular/router';
import { RequestAllowanceService } from '../../../../../services/request-allowance.service';

import { InfoRequestAllowanceComponent } from './info-request-allowance/info-request-allowance.component';  

//import { UserInfoValidationService } from '../../../../../services/user-info-validation.service';

import { RecommendationService } from '../../../../../services/recommendation.service';

import { NaturalpersonService } from '../../../../../services/naturalperson.service';


@Component({
  selector: 'app-request-allowance',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, ToastModule, InfoRequestAllowanceComponent],
  providers: [MessageService],
  templateUrl: './request-allowance.component.html',
  styleUrls: ['./request-allowance.component.css']
})
export class RequestAllowanceComponent implements OnInit {
  allowanceForm!: FormGroup;
  displayMessageNatPerson: string = '';
  isAdditionalDisabled: boolean = false;

  pdfFile: File | null = null;
  isSubmitting: boolean = false;

  constructor(
    private fb: FormBuilder,
    private allowanceService: RequestAllowanceService,
    private loginService: LoginService,
    private messageService: MessageService,
    private router: Router,
    //private userInfoValidationService: UserInfoValidationService,

    private recommendationService: RecommendationService,
    private naturalpersonService: NaturalpersonService  
  ) {
    this.allowanceForm = this.fb.group({
      idTipoAuxilio: [null, Validators.required],
      descripcion:[null],
      rutaDocumento: [null, Validators.required]
    });
  }

  ngOnInit(): void {
    this.validateUserRecords();

    
  }

  validateUserRecords(): void {
    const token = this.loginService.getTokenClaims();

    if (token) {
        // Inicialmente, deshabilitamos la solicitud de crédito hasta que todas las validaciones pasen
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
      const file = input.files[0];
      if (file.type === 'application/pdf') {
        this.pdfFile = file;
        this.allowanceForm.patchValue({ rutaDocumento: this.pdfFile });
        this.allowanceForm.get('rutaDocumento')?.updateValueAndValidity();
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
  

  onSubmit(): void {
    if (this.allowanceForm.valid) {
      this.isSubmitting = true;
      const token = this.loginService.getTokenClaims();

      if(token) {
        const userId = token.userId;
        const formData = new FormData()

    
        formData.append('idUsuario', userId);

        formData.append('idTipoAuxilio', this.allowanceForm.value.idTipoAuxilio);
        formData.append('descripcion', this.allowanceForm.value.descripcion ?? '');
    

        if (this.pdfFile) {
          formData.append('rutaDocumento', this.pdfFile, this.pdfFile.name);
        } else {
          this.messageService.add({ severity: 'error', summary: 'Error', detail: 'Debe subir la copia del documento' });
          this.isSubmitting = false;
          return;
        }

        this.allowanceService.create(formData).subscribe({
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
}