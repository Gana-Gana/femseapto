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

@Component({
  selector: 'app-allowance-requests',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, GenerateAllowanceRequestComponent, AllowanceReportComponent, TableModule, TagModule, IconFieldModule, InputTextModule, InputIconModule, MultiSelectModule, DropdownModule, HttpClientModule, InputGroupModule, ToolbarModule, InputGroupAddonModule, ButtonModule],
  templateUrl: './allowance-requests.component.html',
  styleUrl: './allowance-requests.component.css',
  encapsulation: ViewEncapsulation.None
})
export class AllowanceRequestsComponent implements OnInit {
@ViewChild('dt2') dt2!: Table;

userRole: number = 4;

  ngOnInit(): void {
    const role = localStorage.getItem('userRole');
    if (role) {
      this.userRole = parseInt(role, 10);
    }
    console.log('userRole:', this.userRole);
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

  loadAllowanceRequests(page: number = 1, size: number = 10, search: string = '', date: string = ''): void {
    this.loading = true;
    this.requestAllowanceService.getAll({ page, size, search, date }).subscribe({
      next: response => {
        this.allowanceRequests = response.data.map((request: any) => ({
          ...request
          //montoSolicitado: this.formatNumber(request.montoSolicitado), //EYYYYY ESTO TOCA CAMBIARLO
          //valorCuotaQuincenal: this.formatNumber(request.valorCuotaQuincenal), //EYYYYY ESTO TOCA CAMBIARLO TAMBIENNNNNNNNNNNNNNNNN
        }));
        this.totalRecords = response.total;
        this.loading = false;
      },
      error: err => {
        console.error('Error al cargar solicitudes de auxilios', err);
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.searchQuery = this.searchControl.value || '';
    this.dateQuery = this.dateControl.value || '';
    this.loadAllowanceRequests(this.currentPage, this.rows, this.searchQuery, this.dateQuery);
  }

  onPageChange(page: number): void {
    this.currentPage = page;
    this.loadAllowanceRequests(this.currentPage, this.rows, this.searchQuery, this.dateQuery);
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
      minimumFractionDigits: 0
    }).format(numericValue);
  }
}
