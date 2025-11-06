import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FeaturedCities } from './featured-cities';

describe('FeaturedCities', () => {
  let component: FeaturedCities;
  let fixture: ComponentFixture<FeaturedCities>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FeaturedCities]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FeaturedCities);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
