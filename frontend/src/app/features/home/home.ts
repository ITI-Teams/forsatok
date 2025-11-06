import { Component } from '@angular/core';
import { Hero } from '../home-parts/hero/hero';
import { HowItWork } from "../home-parts/how-it-work/how-it-work";

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [Hero, HowItWork],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home {

}
