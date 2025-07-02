import { Component, Input, OnInit } from '@angular/core';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { firstValueFrom, forkJoin } from 'rxjs';
import { AllowanceTypeService } from '../../../../../services/allowance-type.service';
import { UserService } from '../../../../../services/user.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { RequestAllowanceService } from '../../../../../services/request-allowance.service';

@Component({
  selector: 'app-generate-allowance-request',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './generate-allowance-request.component.html',
  styleUrl: './generate-allowance-request.component.css',
})
export class GenerateAllowanceRequestComponent implements OnInit {
  userRole: number = 0;
  @Input() userId: number = 0;
  @Input() idSolicitudAuxilio: number = 0;
  isLoading: boolean = false;

  nombreAsociado: string = '';
  numeroDocumento: number = 0;
  idTipoAuxilio: string = '';
  descripcion: string = '';
  dependenciaEmpresa: string = '';
  cargoOcupa: string = '';
  celular: string = '';
  direccionResidencia: string = '';

  constructor(
    private http: HttpClient,
    private allowanceTypeService: AllowanceTypeService,
    private userService: UserService,
    private naturalPersonService: NaturalpersonService,
    private requestAllowanceService: RequestAllowanceService
  ) {}

  ngOnInit(): void {
      const role = localStorage.getItem('userRole');
      if (role) {
        this.userRole = parseInt(role, 10);
      }
    }

  async loadData() {
    if (this.idSolicitudAuxilio) {
      const allowance = await firstValueFrom(
        this.requestAllowanceService.getById(this.idSolicitudAuxilio)
      );
      this.descripcion = allowance.descripcion;

      if (allowance.idTipoAuxilio) {
        this.idTipoAuxilio = allowance.idTipoAuxilio?.toString(); // usa el ID directamente
      } else {
        console.warn('⚠️ No hay idTipoAuxilio en la solicitud.');
      }
    }

    if (this.userId) {
      const user = await firstValueFrom(this.userService.getById(this.userId));
      this.nombreAsociado = `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
      this.numeroDocumento = user.numeroDocumento;

      const person = await firstValueFrom(
        this.naturalPersonService.getByUserId(this.userId)
      );
      this.dependenciaEmpresa = person.dependencia_empresa || person.dependenciaEmpresa || '';
      this.cargoOcupa = person.cargo_ocupa || person.cargoOcupa || '';
      this.celular = person.celular;
      this.direccionResidencia = person.direccionResidencia;
    }
  }

  async generateExcel() {
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadTemplate(workbook);
      const worksheet = workbook.getWorksheet(1);

      if (worksheet) {

        await worksheet.protect('femseapto2024', { 
          selectLockedCells: true, 
          selectUnlockedCells: true 
        });
        
        worksheet.getCell('C7').value = this.nombreAsociado;     
        worksheet.getCell('L7').value = this.numeroDocumento;      

        worksheet.getCell('E8').value = this.dependenciaEmpresa + ' - ' + this.cargoOcupa;
        worksheet.getCell('L8').value = this.celular;                 

        worksheet.getCell('D9').value = this.direccionResidencia;    

        worksheet.getCell('A14').value = this.descripcion;             

        const tipo = this.idTipoAuxilio;

        switch (tipo) {
          case '1':
            worksheet.getCell('A11').value = 'X'; // Maternidad
            break;
          case '2':
            worksheet.getCell('A12').value = 'X'; // Lentes y monturas
            break;
          case '6':
            worksheet.getCell('E11').value = 'X'; // Educación
            break;
          case '3':
            worksheet.getCell('E12').value = 'X'; // Calamidad
            break;
          case '4':
            worksheet.getCell('I11').value = 'X'; // Tratamientos
            break;
          case '5':
            worksheet.getCell('I12').value = 'X'; // Gastos notariales
            break;
          default:
            console.warn('❌ Tipo de auxilio no reconocido:', tipo);
        }

      }

      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, `Solicitud_Auxilio_${this.numeroDocumento}.xlsx`);
    } catch (error) {
      console.error('Error generating Excel:', error);
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get('/assets/SOLICITAR_AUXILIO.xlsx', {
          responseType: 'arraybuffer',
        })
      );
      await workbook.xlsx.load(data);
    } catch (error) {
      console.error('Error reading template file:', error);
      throw error;
    }
  }

  async onGenerateClick() {
    this.isLoading = true;

    try {
      await this.loadData();

      await this.generateExcel();
    } catch (error) {
    } finally {
      this.isLoading = false;
    }
  }
}