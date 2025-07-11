import { Component } from '@angular/core';

// Components
import { CarouselComponent } from '../home/components/carousel/carousel.component';
import { CreditServiceHomeComponent } from '../home/components/credit-service-home/credit-service-home.component';
import { SavingServiceHomeComponent } from '../home/components/saving-service-home/saving-service-home.component';
import { AllowanceServiceComponent } from '../home/components/allowance-service/allowance-service.component'; 

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CarouselComponent, CreditServiceHomeComponent, SavingServiceHomeComponent, AllowanceServiceComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {

}
