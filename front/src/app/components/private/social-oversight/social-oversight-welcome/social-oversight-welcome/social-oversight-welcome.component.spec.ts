import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SocialOversightWelcomeComponent } from './social-oversight-welcome.component';

describe('SocialOversightWelcomeComponent', () => {
  let component: SocialOversightWelcomeComponent;
  let fixture: ComponentFixture<SocialOversightWelcomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SocialOversightWelcomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SocialOversightWelcomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
