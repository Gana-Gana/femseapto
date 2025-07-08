import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterOutlet, NavigationEnd } from '@angular/router';
import { filter } from 'rxjs/operators';
import { InactivityService } from './services/inactivity.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'front';

  constructor(
    private router: Router,
    private inactivityService: InactivityService
  ) {
    // console.log("%c App component initialized", "color: green; font-size: 16px; font-weight: bold");
    // console.log("%c Timeout value: " + this.inactivityService.timeoutInMs + " ms", "color: blue; font-size: 14px");
    
    this.inactivityService.startMonitoring();    
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe(() => {
      window.scrollTo(0, 0);
    });
  }
}
