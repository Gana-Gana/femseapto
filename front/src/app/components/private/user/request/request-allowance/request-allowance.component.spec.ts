import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RequestAllowanceComponent } from './request-allowance.component';

describe('RequestAllowanceComponent', () => {
  let component: RequestAllowanceComponent;
  let fixture: ComponentFixture<RequestAllowanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RequestAllowanceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RequestAllowanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
