import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { JwtHelperService } from '@auth0/angular-jwt';
import { LoginService } from '../services/login.service';

@Injectable({
  providedIn: 'root'
})
export class SocialOversightGuard implements CanActivate {

  constructor(
    private cookieService: CookieService,
    private router: Router,
    private jwtHelper: JwtHelperService,
    private loginService: LoginService
  ) {}

  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): boolean {
    const token = this.cookieService.get('auth_token');
    if (token) {
      try {
        const decodedToken = this.jwtHelper.decodeToken(token);
        if (!this.jwtHelper.isTokenExpired(token)) {
          if (decodedToken.id_rol === 4) {
            return true;
          } else if (decodedToken.id_rol === 2) {
            this.router.navigate(['/auth/user']);
            return false;
          }
            else if (decodedToken.id_rol === 1) {
            this.router.navigate(['/auth/admin']);
            return false;
          }
        }
      } catch (error) {
        console.error('Error decoding token:', error);
      }
    }

    this.router.navigate(['/login']);
    return false;
  }
}
