import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GenerateAllowanceRequestComponent } from './generate-allowance-request.component';

describe('GenerateAllowanceRequestComponent', () => {
  let component: GenerateAllowanceRequestComponent;
  let fixture: ComponentFixture<GenerateAllowanceRequestComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GenerateAllowanceRequestComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(GenerateAllowanceRequestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
