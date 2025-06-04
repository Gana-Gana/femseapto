import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { HttpClient, HttpParams } from '@angular/common/http';
import { firstValueFrom, Observable } from 'rxjs';
import saveAs from 'file-saver';

@Injectable({
  providedIn: 'root',
})
export class RequestAllowanceService {
  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) {}

  create(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/solicitudesauxilios.php`, formData, {
      withCredentials: true
    });
  }

  getAll(params: {
    page: number;
    size: number;
    search?: string;
    date?: string;
  }): Observable<{ data: any[]; total: number }> {
    let httpParams = new HttpParams()
      .set('page', params.page.toString())
      .set('size', params.size.toString());

    if (params.search) {
      httpParams = httpParams.set('search', params.search);
    }
    if (params.date) {
      httpParams = httpParams.set('date', params.date);
    }

    return this.http.get<{ data: any[]; total: number }>(
      `${this.apiUrl}/solicitudesauxilios.php`,
      { params: httpParams, withCredentials: true }
    );
  }

  getById(id: number): Observable<any> {
    return this.http.get<any>(
      `${this.apiUrl}/solicitudesauxilios.php?id=${id}`,
      { withCredentials: true }
    );
  }

  getAllowancesByDateRange(startDate: string, endDate: string): Observable<any[]> {
    const params = new HttpParams()
      .set('startDate', startDate)
      .set('endDate', endDate);
    return this.http.get<any[]>(`${this.apiUrl}/solicitudesauxilios.php`, {
      params,
      withCredentials: true,
    });
  }

  async downloadAllowanceRequestPdf(
    idSolicitudAuxilio: number,
    numeroDocumento: number
  ): Promise<void> {
    try {
      const pdfData = await firstValueFrom(
        this.http.get(
          `${this.apiUrl}/solicitudesauxilios.php?id=${idSolicitudAuxilio}&download=pdf`,
          {
            withCredentials: true,
            responseType: 'blob',
          }
        )
      );
      saveAs(
        pdfData,
        `Solicitud_Auxilio_${idSolicitudAuxilio}_${numeroDocumento}.pdf`
      );
    } catch (error) {
      console.error('Error al descargar el PDF:', error);
    }
  }
}