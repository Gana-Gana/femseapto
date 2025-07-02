import { Component, OnInit, ViewChild, ViewEncapsulation } from '@angular/core';
import { RequestAllowanceService } from '../../../../../services/request-allowance.service';
import { UserService, User } from '../../../../../services/user.service';
import { CommonModule } from '@angular/common';
import { FormControl, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { GenerateAllowanceRequestComponent } from '../generate-allowance-request/generate-allowance-request.component';
import { AllowanceReportComponent } from '../allowance-report/allowance-report.component';
import { forkJoin } from 'rxjs';
import { AllowanceTypeService } from '../../../../../services/allowance-type.service';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';

import { Table, TableModule } from 'primeng/table';
import { TagModule } from 'primeng/tag';
import { IconFieldModule } from 'primeng/iconfield';
import { InputIconModule } from 'primeng/inputicon';
import { HttpClientModule } from '@angular/common/http';
import { InputTextModule } from 'primeng/inputtext';
import { MultiSelectModule } from 'primeng/multiselect';
import { DropdownModule } from 'primeng/dropdown';
import { InputGroupModule } from 'primeng/inputgroup';
import { ToolbarModule } from 'primeng/toolbar';
import { InputGroupAddonModule } from 'primeng/inputgroupaddon';
import { ButtonModule } from 'primeng/button';
import { DialogModule } from 'primeng/dialog';

@Component({
  selector: 'app-allowance-requests',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FormsModule,
    AllowanceReportComponent,
    GenerateAllowanceRequestComponent,
    TableModule,
    TagModule,
    IconFieldModule,
    InputTextModule,
    InputIconModule,
    MultiSelectModule,
    DropdownModule,
    HttpClientModule,
    InputGroupModule,
    ToolbarModule,
    InputGroupAddonModule,
    ButtonModule,
    DialogModule
  ],
  templateUrl: './allowance-requests.component.html',
  styleUrl: './allowance-requests.component.css',
  encapsulation: ViewEncapsulation.None,
})
export class AllowanceRequestsComponent implements OnInit {
  @ViewChild('dt2') dt2!: Table;

  userRole: number = 0;

  ngOnInit(): void {
  const role = localStorage.getItem('userRole');

  this.loadAllowanceRequests();
}


  allowanceRequests: any[] = [];

  searchControl: FormControl;
  dateControl: FormControl;

  totalRecords: number = 0;
  loading: boolean = true;
  searchQuery: string = '';
  dateQuery: string = '';
  rows: number = 10;
  currentPage: number = 1;
  totalPages: number = 0;
  pages: number[] = [];

  constructor(
    private requestAllowanceService: RequestAllowanceService,
    private userService: UserService,
    private allowanceTypeService: AllowanceTypeService
  ) {
    this.searchControl = new FormControl('');
    this.dateControl = new FormControl('');
  }

  onFilterGlobal(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target) {
      this.dt2.filterGlobal(target.value, 'contains');
    }
  }

  loadAllowanceRequests(
  page: number = 1,
  size: number = 10,
  search: string = '',
  date: string = ''
  ): void {
    this.loading = true;
    this.requestAllowanceService
      .getAll({ page, size, search, date })
      .subscribe({
        next: (response) => {
          this.allowanceRequests = response.data.map((request: any) => {
            if (typeof request.adjuntos_auxilio === 'string') {
              try {
                request.adjuntos_auxilio = JSON.parse(request.adjuntos_auxilio);
              } catch (e) {
                request.adjuntos_auxilio = [];
              }
            }

            return {
              ...request,
            };
          });
          this.totalRecords = response.total;
          this.loading = false;
        },
        error: (err) => {
          console.error('Error al cargar solicitudes de auxilios', err);
          this.loading = false;
        },
      });
  }
  
  onSearch(): void {
    this.currentPage = 1;
    this.searchQuery = this.searchControl.value || '';
    this.dateQuery = this.dateControl.value || '';
    this.loadAllowanceRequests(
      this.currentPage,
      this.rows,
      this.searchQuery,
      this.dateQuery
    );
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadAllowanceRequests(
      this.currentPage,
      this.rows,
      this.searchQuery,
      this.dateQuery
    );
  }

  onClear(): void {
    this.searchControl.setValue('');
    this.dateControl.setValue('');
    this.currentPage = 1;
    this.loadAllowanceRequests();
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.'));
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
    }).format(numericValue);
  }

  acceptRequest(id: number): void {
    console.log(`Solicitud ${id} aceptada.`);
    alert(`Solicitud ${id} aceptada.`);
  }

  rejectRequest(id: number): void {
    console.log(`Solicitud ${id} rechazada.`);
    alert(`Solicitud ${id} rechazada.`);
  }

  addObservation(id: number): void {
    const observation = prompt(
      `Escribe una observación para la solicitud ${id}:`
    );
    if (observation) {
      console.log(`Observación para ${id}:`, observation);
      alert(`Observación registrada.`);
    }
  }
  
  viewAttachments(adjuntos: string[]): void {
    if (!adjuntos || adjuntos.length === 0) {
      alert('No hay documentos adjuntos.');
      return;
    }

    adjuntos.forEach((archivo: string) => {
      window.open(archivo, '_blank');
    });
  }

  selectedRequest: any = null;
  showEditModal: boolean = false;
  selectedStatus: any = null;
  commentText: string = '';
  successfullyManaged: boolean | null = null;

  openEditModal(request: any) {
    this.selectedRequest = request;
    this.showEditModal = true;
    this.selectedStatus = request.estado;
    this.commentText = request.observaciones || '';
    this.successfullyManaged = false;
  }

  saveStatusChanges() {
    const payload = {
      id: this.selectedRequest.id,
      estado: this.selectedStatus,
      observaciones: this.commentText
    };

    this.requestAllowanceService.updateStatusAndComment(payload).subscribe({
      next: () => {
        const index = this.allowanceRequests.findIndex(r => r.id === this.selectedRequest.id);
        if (index !== -1) {
          this.allowanceRequests[index].estado = this.selectedStatus;
          this.allowanceRequests[index].observaciones = this.commentText;
        }
        this.successfullyManaged = true;
      },
      error: (error) => {
        console.error('Update error:', error);
        this.showEditModal = false;
      }
    });
  }

  closeModal() {
    this.showEditModal = false;
    this.selectedRequest = null;
    this.selectedStatus = null;
    this.commentText = '';
    this.successfullyManaged = null;
  }

  mostrarModal: boolean = false;
  adjuntosSeleccionados: string[] = [];

  OpenModalAttachments(adjuntos: string[]) {
    this.adjuntosSeleccionados = adjuntos;
    this.mostrarModal = true;
  }

  SeeAttachments(archivo: string) {
    const url = `http://localhost/femseapto/uploads/${archivo}`;
    window.open(url, '_blank');
  }

  DownloadAttachments(archivo: string) {
    const url = `http://localhost/femseapto/uploads/${archivo}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = archivo.split('/').pop() || 'archivo';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

}