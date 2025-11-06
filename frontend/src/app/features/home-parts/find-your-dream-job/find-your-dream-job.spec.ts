import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FindYourDreamJob } from './find-your-dream-job';

describe('FindYourDreamJob', () => {
  let component: FindYourDreamJob;
  let fixture: ComponentFixture<FindYourDreamJob>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FindYourDreamJob]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FindYourDreamJob);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
