import { Injectable, NgZone } from '@angular/core';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class InactivityService {
    
  private timeoutInMs = 60 * 60 * 1000;
  private inactivityTimer: any;

  constructor(private router: Router, private ngZone: NgZone) {
    this.startMonitoring();
  }

  startMonitoring() {
    console.log("Inactivity monitoring started");
    this.resetTimer();

    ['mousemove', 'keydown', 'mousedown', 'touchstart'].forEach(event => {
      window.addEventListener(event, () => this.resetTimer());
    });
  }

  resetTimer() {
    clearTimeout(this.inactivityTimer);
    this.inactivityTimer = setTimeout(() => this.logout(), this.timeoutInMs);
    console.log("Inactivity timer reset");
  }

  logout() {
    console.log("User logged out due to inactivity");
    localStorage.removeItem('auth_token');
    document.cookie = 'auth_token=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    this.router.navigate(['/login']);
  }
}
