import { Component, Input, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { UserService, User } from '../../../../../services/user.service';
import { NaturalpersonService } from '../../../../../services/naturalperson.service';
import { CitiesService } from '../../../../../services/cities.service';
import { SolicitudAhorroService } from '../../../../../services/request-saving.service';
import { CompanyService } from '../../../../../services/company.service';
import { FinancialInfoService } from '../../../../../services/financial-info.service';
import { firstValueFrom } from 'rxjs';
import * as ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-generate-saving-request',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './generate-saving-request.component.html',
  styleUrls: ['./generate-saving-request.component.css'],
})
export class GenerateSavingRequestComponent implements OnInit {
  @Input() userId: number = 0;
  @Input() idSolicitudAhorro: number = 0;

  isLoading: boolean = false;

  defaultFont: { underline: boolean; name: string; size: number };

  nombreCompleto: string = '';
  numeroDocumento: number = 0;
  municipioExpedicionDocumento: string = '';
  valorTotalAhorro: number = 0;
  quincena: string = '';
  mes: string = '';
  celular: string = '';
  nombreEmpresa: string = '';
  salario: number = 0;
  lineasAhorro: any[] = [];
  tipoAsociado: number = 0;

  constructor(
    private http: HttpClient,
    private userService: UserService,
    private naturalPersonService: NaturalpersonService,
    private citiesService: CitiesService,
    private solicitudAhorroService: SolicitudAhorroService,
    private companyService: CompanyService,
    private financialInfoService: FinancialInfoService
  ) {
    this.defaultFont = { underline: false, name: 'Calibri', size: 10 };
  }

  ngOnInit() {
  }

  async loadData() {
    const user = await firstValueFrom(this.userService.getById(this.userId));
    this.nombreCompleto = `${user.primerNombre || ''} ${user.segundoNombre || ''} ${user.primerApellido || ''} ${user.segundoApellido || ''}`.trim();
    this.numeroDocumento = Number(user.numeroDocumento);
    this.tipoAsociado = user.id_tipo_asociado;

    const person = await firstValueFrom(this.naturalPersonService.getByUserId(this.userId));
    this.celular = person.celular;

    const city = await firstValueFrom(this.citiesService.getById(person.mpioExpDoc));
    this.municipioExpedicionDocumento = city.nombre;

    const company = await firstValueFrom(this.companyService.getById(person.idEmpresaLabor));
    this.nombreEmpresa = company.nombre;

    const solicitud = await firstValueFrom(this.solicitudAhorroService.getById(this.idSolicitudAhorro));
    this.valorTotalAhorro = solicitud.montoTotalAhorrar;
    this.quincena = solicitud.quincena;
    this.mes = solicitud.mes;
    this.lineasAhorro = solicitud.lineas || [];

    const financialInfo = await firstValueFrom(this.financialInfoService.getByUserId(this.userId));
    this.salario = financialInfo.ingresosMensuales;
  }

  async generateExcel() {
    this.isLoading = true;
    const workbook = new ExcelJS.Workbook();
    try {
      await this.loadData();
      const templateUrl = this.tipoAsociado === 1 ? '/assets/SOLICITAR_AHORRO_NOMINA.xlsx' : '/assets/SOLICITAR_AHORRO_COMISION.xlsx';
      await this.loadTemplate(workbook, templateUrl);
      const worksheet = workbook.getWorksheet(1);

      if (worksheet) {

        await worksheet.protect('femseapto2024', { 
          selectLockedCells: true, 
          selectUnlockedCells: true 
        });

        const texto = [
          { text: "Yo ", font: this.defaultFont },
          { text: this.nombreCompleto, font: { ...this.defaultFont, underline: true } },
          { text: " Identificado con cédula N° ", font: this.defaultFont },
          { text: this.numeroDocumento.toString(), font: { ...this.defaultFont, underline: true } },
          { text: " de ", font: this.defaultFont },
          { text: this.municipioExpedicionDocumento, font: { ...this.defaultFont, underline: true } },
          { text: ", autorizo a ", font: this.defaultFont },
          { text: this.nombreEmpresa, font: { ...this.defaultFont, underline: true } },
          { text: " para descontar de mi salario el valor de ", font: this.defaultFont },
          { text: this.formatNumber(this.valorTotalAhorro.toString()), font: { ...this.defaultFont, underline: true } },
          { text: " quincenalmente, a partir de la ", font: this.defaultFont },
          { text: this.quincena, font: { ...this.defaultFont, underline: true } },
          { text: " quincena de ", font: this.defaultFont },
          { text: this.mes, font: { ...this.defaultFont, underline: true } },
          { text: ".", font: this.defaultFont }
        ];

        worksheet.getCell('B5').value = { richText: texto };

        if (this.tipoAsociado === 1) {
          const salarioTexto = [
            { text: "Salario ", font: this.defaultFont },
            { text: this.formatNumber(this.salario.toString()), font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B6').value = { richText: salarioTexto };

          const nombreTexto = [
            { text: "NOMBRE: ", font: this.defaultFont },
            { text: this.nombreCompleto, font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B13').value = { richText: nombreTexto };

          const cedulaTexto = [
            { text: "CEDULA: ", font: this.defaultFont },
            { text: this.numeroDocumento.toString(), font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B14').value = { richText: cedulaTexto };

          const celularTexto = [
            { text: "CELULAR: ", font: this.defaultFont },
            { text: this.celular, font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B15').value = { richText: celularTexto };

          this.lineasAhorro.forEach(linea => {
            let cellAddress = '';
            let label = '';
            switch (linea.idLineaAhorro) {
              case 1:
                cellAddress = 'D11';
                label = 'Vivienda: ';
                break;
              case 2:
                cellAddress = 'D9';
                label = 'Navideño: ';
                break;
              case 3:
                cellAddress = 'D10';
                label = 'Vacacional: ';
                break;
              case 4:
                cellAddress = 'D8';
                label = 'Extraordinario: ';
                break;
            }
            if (cellAddress) {
              worksheet.getCell(cellAddress).value = 'X';
            }
            if (label && linea.montoAhorrar !== undefined) {
              worksheet.getCell(`E${cellAddress.slice(1)}`).value = {
                richText: [
                  { text: label, font: this.defaultFont },
                  { text: this.formatNumber(linea.montoAhorrar.toString()), font: { ...this.defaultFont, underline: true } }
                ]
              };
            }
          });
        } else if (this.tipoAsociado === 2) {
          const nombreTexto = [
            { text: "NOMBRE: ", font: this.defaultFont },
            { text: this.nombreCompleto, font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B10').value = { richText: nombreTexto };

          const cedulaTexto = [
            { text: "CEDULA: ", font: this.defaultFont },
            { text: this.numeroDocumento.toString(), font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B11').value = { richText: cedulaTexto };

          const celularTexto = [
            { text: "CELULAR: ", font: this.defaultFont },
            { text: this.celular, font: { ...this.defaultFont, underline: true } }
          ];
          worksheet.getCell('B12').value = { richText: celularTexto };

          const linea = this.lineasAhorro.find(linea => linea.idLineaAhorro === 4);
          if (linea) {
            worksheet.getCell('D8').value = 'X';
            worksheet.getCell('E8').value = {
              richText: [
                { text: 'Extraordinario: ', font: this.defaultFont },
                { text: this.formatNumber(linea.montoAhorrar.toString()), font: { ...this.defaultFont, underline: true } }
              ]
            };
          }
        }

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], {
          type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        });
        saveAs(blob, `Solicitud de Ahorro ${this.numeroDocumento}.xlsx`);
      } else {
        console.error('Worksheet is undefined.');
      }
    } catch (error) {
      console.error('Error loading template', error);
    } finally {
      this.isLoading = false;
    }
  }

  async loadTemplate(workbook: ExcelJS.Workbook, templateUrl: string) {
    try {
      const data: ArrayBuffer = await firstValueFrom(
        this.http.get(templateUrl, {
          responseType: 'arraybuffer',
        })
      );
      await workbook.xlsx.load(data);
    } catch (error) {
      console.error('Error reading the template file:', error);
      throw error;
    }
  }

  formatNumber(value: string): string {
    const numericValue = parseFloat(value.replace(',', '.'));
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0
    }).format(numericValue);
  }

  onGenerateClick() {
    this.isLoading = true;
    this.generateExcel();
  }
}