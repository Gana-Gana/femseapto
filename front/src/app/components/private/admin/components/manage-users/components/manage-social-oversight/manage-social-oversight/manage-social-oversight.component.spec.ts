import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageSocialOversightComponent } from './manage-social-oversight.component';

describe('ManageSocialOversightComponent', () => {
  let component: ManageSocialOversightComponent;
  let fixture: ComponentFixture<ManageSocialOversightComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageSocialOversightComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ManageSocialOversightComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
