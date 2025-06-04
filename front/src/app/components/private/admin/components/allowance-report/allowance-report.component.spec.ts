import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AllowanceReportComponent } from './allowance-report.component';

describe('AllowanceReportComponent', () => {
  let component: AllowanceReportComponent;
  let fixture: ComponentFixture<AllowanceReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AllowanceReportComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AllowanceReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
