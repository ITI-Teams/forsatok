import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Employers } from './employers';

describe('Employers', () => {
  let component: Employers;
  let fixture: ComponentFixture<Employers>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Employers]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Employers);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
