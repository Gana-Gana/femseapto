import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AllowanceRequestsComponent } from './allowance-requests.component';

describe('AllowanceRequestsComponent', () => {
  let component: AllowanceRequestsComponent;
  let fixture: ComponentFixture<AllowanceRequestsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AllowanceRequestsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AllowanceRequestsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
