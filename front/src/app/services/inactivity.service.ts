import { Injectable, NgZone } from '@angular/core';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class InactivityService {
  
    
  public timeoutInMs = 60 * 60 * 1000; 
  private inactivityTimer: any;
  constructor(private router: Router, private ngZone: NgZone) {
    // console.log("%c InactivityService initialized", "color: green; font-size: 14px; font-weight: bold");
    // console.log("%c Timeout configurado en: " + (this.timeoutInMs / 1000 / 60) + " minutos", "color: blue; font-size: 14px");
    // if (this.timeoutInMs !== 60 * 60 * 1000) {
    //   console.warn("%c ADVERTENCIA: El tiempo de inactividad no está configurado a 1 hora", "color: red; font-size: 16px; font-weight: bold");
    // }
  }
  startMonitoring() {
    // console.log("%c Inactivity monitoring started", "color: red; font-size: 14px; font-weight: bold");
    this.resetTimer();

    ['mousemove', 'keydown', 'mousedown', 'touchstart'].forEach(event => {
      window.addEventListener(event, () => {
        this.ngZone.run(() => {
          // console.log(`%c Activity detected: ${event}`, "color: orange");
          this.resetTimer();
        });
      });
    });
  }

  resetTimer() {
    clearTimeout(this.inactivityTimer);
    // console.log(`Resetting inactivity timer. Timeout set to: ${this.timeoutInMs} ms`);
    this.inactivityTimer = setTimeout(() => {
      // console.log("Inactivity timer triggered. Logging out.");
      this.logout();
    }, this.timeoutInMs);
  }

  logout() {
    // console.log("User logged out due to inactivity");
    localStorage.removeItem('auth_token');
    document.cookie = 'auth_token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    this.router.navigate(['/login']);
  }
}
