import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FeaturedCandidates } from './featured-candidates';

describe('FeaturedCandidates', () => {
  let component: FeaturedCandidates;
  let fixture: ComponentFixture<FeaturedCandidates>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FeaturedCandidates]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FeaturedCandidates);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
