import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';


import { environment } from '../../environments/environment';

export interface TipoAuxilio {
  id: number;
  nombre: string;
}

@Injectable({
  providedIn: 'root',
})
export class AllowanceTypeService {

  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getTiposDisponibles(): Observable<TipoAuxilio[]> {
  return this.http.get<TipoAuxilio[]>(`${this.apiUrl}/tiposauxilio.php`, {
    withCredentials: true
  });
}


  
  getById(id: number): Observable<TipoAuxilio> {
  return this.http.get<TipoAuxilio>(`${this.apiUrl}/tiposauxilio.php?id=${id}`);
}

getNameById(id: number): Observable<string> {
    return this.http.get<any>(`${this.apiUrl}/tiposauxilio.php?id=${id}`).pipe(
      map((response) => response.nombre)
    );
  }
  
}