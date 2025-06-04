import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

// Environment component
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class AllowanceTypeService {
  private apiUrl: string = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getAll(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/tiposauxilios.php`);
  }

  getById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/tiposauxilios.php?id=${id}`);
  }

  getNameById(id: number): Observable<string> {
    return this.http.get<any>(`${this.apiUrl}/tiposauxilios.php?id=${id}`).pipe(
      map((response) => response.nombre)
    );
  }

  create(data: any): Observable<any> {
    return this.http.post<any>(
      `${this.apiUrl}/tiposauxilios.php`,
      data
    );
  }

  update(id: number, data: any): Observable<any> {
    return this.http.put<any>(
      `${this.apiUrl}/tiposauxilios.php?id=${id}`,
      data
    );
  }
}