import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AllowanceServiceComponent } from './allowance-service.component';

describe('AllowanceServiceComponent', () => {
  let component: AllowanceServiceComponent;
  let fixture: ComponentFixture<AllowanceServiceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AllowanceServiceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AllowanceServiceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
