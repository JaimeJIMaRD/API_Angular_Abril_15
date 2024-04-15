import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MyIndexComponent } from './myindex.component';

describe('IndexComponent', () => {
  let component: MyIndexComponent;
  let fixture: ComponentFixture<MyIndexComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MyIndexComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MyIndexComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
