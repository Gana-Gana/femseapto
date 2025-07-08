import { Component, Input, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ToastModule } from 'primeng/toast';
import { MessageService } from 'primeng/api';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { InputMaskModule } from 'primeng/inputmask';
import { Gender, GenderService } from '../../../../../../services/gender.service';
import { AllowanceTypeService, TipoAuxilio } from '../../../../../../services/allowance-type.service';
import { Zone, ZoneService } from '../../../../../../services/zone.service';
import { HouseType, HouseTypeService,} from '../../../../../../services/house-type.service';
import {MaritalStatus, MaritalStatusService,} from '../../../../../../services/marital-status.service';
import {EducationLevel,  EducationLevelService,} from '../../../../../../services/education-level.service';
import {Company,  CompanyService,} from '../../../../../../services/company.service';
import {ContractType, ContractTypeService,} from '../../../../../../services/contract-type.service';
import {CountriesService,Country,} from '../../../../../../services/countries.service';
import {Department, DepartmentsService,} from '../../../../../../services/departments.service';
import { CitiesService, City } from '../../../../../../services/cities.service';
import {NaturalPerson,NaturalpersonService,} from '../../../../../../services/naturalperson.service';
import { RequestAllowanceComponent } from '../request-allowance.component';
import { LoginService } from '../../../../../../services/login.service';
@Component({
  selector: 'app-info-request-allowance',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ToastModule
  ],
  templateUrl: './info-request-allowance.component.html',
  styleUrl: './info-request-allowance.component.css'
})
export class InfoRequestAllowanceComponent implements OnInit{

  infoForm: FormGroup;
  userId: number | null = null;

  isSubmitting: boolean = false;
 
constructor(
    private fb: FormBuilder,
        private naturalPersonService: NaturalpersonService,
        private loginService: LoginService,
        private messageService: MessageService
      ) {
        this.infoForm = this.fb.group({
          id: [null],
          idUsuario: ['', Validators.required],
          idGenero: [null],
          fechaExpDoc: [null],
          idDeptoExpDoc: [null],
          mpioExpDoc: [null],
          fechaNacimiento: [null],
          paisNacimiento: [null],
          idDeptoNacimiento: [null],
          mpioNacimiento: [null],
          otroLugarNacimiento: [null],
          idDeptoResidencia: [null],
          mpioResidencia: [null],
          idZonaResidencia: [null],
          idTipoVivienda: [null],
          estrato: [null],
          direccionResidencia: ['', Validators.required],
          antigVivienda: [null],
          idEstadoCivil: [null],
          cabezaFamilia: [null],
          personasACargo: [null],
          tieneHijos: [null],
          numeroHijos: [null],
          correoElectronico: [null],
          telefono: [''],
          celular: ['', Validators.required],
          telefonoOficina: [null],
          idNivelEducativo: [null],
          profesion: [null],
          ocupacionOficio: [null],
          idEmpresaLabor: [null],
          idTipoContrato: [null],
          dependenciaEmpresa: ['', Validators.required],
          cargoOcupa: ['', Validators.required],
          jefeInmediato: [null],
          antigEmpresa: [null],
          //mesesAntigEmpresa: ['', Validators.required],
          mesSaleVacaciones: [null],
          nombreEmergencia: [null],
          numeroCedulaEmergencia: [null],
          numeroCelularEmergencia: [null],
        });
      }
    
      ngOnInit(): void {
        this.getUserIdFromToken();
        this.loadInitialData();
      }
    
      getUserIdFromToken(): void {
        const token = this.loginService.getTokenClaims();
        if (token) {
          this.userId = token.userId;
          this.infoForm.patchValue({
            idUsuario: this.userId,
          });
        }
      }
    
      loadInitialData(): void {
        if (this.userId) {
          this.naturalPersonService
            .getByUserId(this.userId)
            .subscribe((natPerson) => {
              this.infoForm.patchValue(natPerson); 
            });
        }
      }
    
      onSubmit(): void {
        if (this.isSubmitting) {
          return;
        }
    
        this.isSubmitting = true;
    
        //console.log("ANTES", this.infoForm.value);
    
        if (this.infoForm.valid) {
          const data: NaturalPerson = this.infoForm.value;
    
          if (data.id) {
            this.naturalPersonService.update(data).subscribe({
              next: () => {
                this.messageService.add({
                  severity: 'success',
                  summary: 'Éxito',
                  detail: 'Información actualizada correctamente',
                });
                setTimeout(() => {
                  this.isSubmitting = false;
                }, 500);
              },
              error: (err) => {
                console.error('Error al actualizar la información', err);
                this.messageService.add({
                  severity: 'error',
                  summary: 'Error',
                  detail:
                    'No se pudo actualizar la información. Vuelve a intentarlo.',
                });
                setTimeout(() => {
                  this.isSubmitting = false;
                }, 500);
              },
            });
          } else {
            this.naturalPersonService.create(data).subscribe({
              next: (response) => {
                //console.log(response);
                this.infoForm.patchValue({ id: response.id });
                this.messageService.add({
                  severity: 'success',
                  summary: 'Éxito',
                  detail: 'Información creada correctamente',
                });
                setTimeout(() => {
                  this.isSubmitting = false;
                }, 500);
              },
              error: (err) => {
                console.error('Error al crear la información', err);
                this.messageService.add({
                  severity: 'error',
                  summary: 'Error',
                  detail: 'No se pudo crear la información. Vuelve a intentarlo.',
                });
                setTimeout(() => {
                  this.isSubmitting = false;
                }, 500);
              },
            });
          }
        } else {
          this.messageService.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Algún dato te falta por registrar.',
          });
          setTimeout(() => {
            this.isSubmitting = false;
          }, 500);
        }
      }
}