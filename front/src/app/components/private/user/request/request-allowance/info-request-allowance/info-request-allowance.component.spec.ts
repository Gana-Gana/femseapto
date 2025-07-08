import { ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoRequestAllowanceComponent } from './info-request-allowance.component';

describe('InfoRequestAllowanceComponent', () => {
  let component: InfoRequestAllowanceComponent;
  let fixture: ComponentFixture<InfoRequestAllowanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [InfoRequestAllowanceComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(InfoRequestAllowanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
