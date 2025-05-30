import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RendementComponent } from './rendement.component';

describe('RendementComponent', () => {
  let component: RendementComponent;
  let fixture: ComponentFixture<RendementComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RendementComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RendementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
