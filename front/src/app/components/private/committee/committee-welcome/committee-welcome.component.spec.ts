import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CommitteeWelcomeComponent } from './committee-welcome.component';

describe('CommitteeWelcomeComponent', () => {
  let component: CommitteeWelcomeComponent;
  let fixture: ComponentFixture<CommitteeWelcomeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CommitteeWelcomeComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CommitteeWelcomeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
