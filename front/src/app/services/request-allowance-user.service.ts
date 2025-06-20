import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, firstValueFrom } from 'rxjs';
import { environment } from '../../environments/environment'; 

@Injectable({
  providedIn: 'root'
})
export class RequestAllowanceUserService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) { }

  create(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/solicitudesauxilio.php`, formData, { withCredentials: true });
  }

  getAll(params: { page: number; size: number; search?: string; date?: string }): Observable<{ data: any[], total: number }> {
    let httpParams = new HttpParams()
      .set('page', params.page.toString())
      .set('size', params.size.toString());

    if (params.search) {
      httpParams = httpParams.set('search', params.search);
    }
    if (params.date) {
      httpParams = httpParams.set('date', params.date);
    }

    return this.http.get<{ data: any[], total: number }>(
      `${this.apiUrl}/solicitudesauxilio.php`, 
      { params: httpParams, withCredentials: true }
    );
  }

  // Obtener una solicitud por su ID
  getById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/solicitudesauxilio.php?id=${id}`, { withCredentials: true });
  }

  // Descargar PDF
  async downloadRequestPdf(idSolicitud: number, numeroDocumento: number): Promise<void> {
    try {
      const pdfData = await firstValueFrom(
        this.http.get(`${this.apiUrl}/solicitudesauxilio.php?id=${idSolicitud}&download=pdf`, {
          withCredentials: true,
          responseType: 'blob'
        })
      );
      // Aquí puedes usar file-saver si lo necesitas como en el otro ejemplo
      // saveAs(pdfData, `Solicitud_Auxilio_${idSolicitud}_${numeroDocumento}.pdf`);
    } catch (error) {
      console.error('Error al descargar el PDF:', error);
    }
  }

}
