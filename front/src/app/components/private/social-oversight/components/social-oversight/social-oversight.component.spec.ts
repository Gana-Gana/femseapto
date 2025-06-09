import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SocialOversightComponent } from './social-oversight.component';

describe('SocialOversightComponent', () => {
  let component: SocialOversightComponent;
  let fixture: ComponentFixture<SocialOversightComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SocialOversightComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SocialOversightComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
