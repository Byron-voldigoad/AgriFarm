import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AssistanceIAComponent } from './assistance-ia.component';

describe('AssistanceIAComponent', () => {
  let component: AssistanceIAComponent;
  let fixture: ComponentFixture<AssistanceIAComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AssistanceIAComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AssistanceIAComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
