import { Header } from '../header/header';
import { HeaderPrivate } from '../header-private/header-private';
import { Footer } from '../footer/footer';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet, Router, NavigationEnd } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { filter, takeUntil } from 'rxjs/operators';
import { Subject } from 'rxjs';

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [RouterOutlet, Header, Footer,HeaderPrivate],
  templateUrl: './layout.html',
  styleUrl: './layout.css',
})
export class Layout implements  OnInit, OnDestroy{
  isLoggedIn = false;
  isPrivateRoute = false;

  private publicRoutes = ['/', '/home', '/login', '/register', '/about', '/contact'];

  private destroy$ = new Subject<void>();

  constructor(
    private auth: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.checkAuthAndRoute();

    this.router.events
      .pipe(
        filter(event => event instanceof NavigationEnd),
        takeUntil(this.destroy$)
      )
      .subscribe(() => {
        this.checkAuthAndRoute();
      });

    this.auth.isLoggedIn$()
      .pipe(takeUntil(this.destroy$))
      .subscribe(status => {
        this.isLoggedIn = status;
        this.checkRouteType();
      });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private checkAuthAndRoute() {
    this.isLoggedIn = this.auth.hasToken();
    this.checkRouteType();
  }

  private checkRouteType() {
    const currentRoute = this.router.url.split('?')[0];
    this.isPrivateRoute = !this.publicRoutes.some(route =>
      currentRoute === route ||
      currentRoute.startsWith(route + '/')
    );
  }

}
