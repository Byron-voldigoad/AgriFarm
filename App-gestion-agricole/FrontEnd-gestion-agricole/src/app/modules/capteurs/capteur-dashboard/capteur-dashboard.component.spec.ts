import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CapteurDashboardComponent } from './capteur-dashboard.component';

describe('CapteurDashboardComponent', () => {
  let component: CapteurDashboardComponent;
  let fixture: ComponentFixture<CapteurDashboardComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CapteurDashboardComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CapteurDashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
