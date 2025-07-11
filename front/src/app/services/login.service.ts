import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { JwtHelperService } from '@auth0/angular-jwt';

// Environment component
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class LoginService {

  private authUrl: string = environment.authUrl;

  private authStatus = new BehaviorSubject<boolean>(false);
  constructor(private http: HttpClient, private jwtHelper: JwtHelperService) {
    this.updateAuthStatus(!this.isTokenExpired());
  }

  login(usuario: string, contrasenia: string): Observable<any> {
    const url = `${this.authUrl}/auth/login.php`;
    return this.http.post(
      url,
      {
        usuario,
        contrasenia,
      },
    );
  }

  updateAuthStatus(status: boolean): void {
    this.authStatus.next(status);
  }

  getAuthStatusListener(): Observable<boolean> {
    return this.authStatus.asObservable();
  }

  isTokenExpired(): boolean {
    const token = localStorage.getItem('auth_token') || '';
    return this.jwtHelper.isTokenExpired(token);
  }

  getTokenClaims(): any {
    const token = localStorage.getItem('auth_token');
    return token ? this.jwtHelper.decodeToken(token) : null;
  }

  logout() {
    return this.http.post(
      `${this.authUrl}/auth/logout.php`,
      { },
      { withCredentials: true }
    );
  }
}