import { Routes } from '@angular/router';
import { DashboardComponent } from './modules/dashboard/dashboard.component';

export const routes: Routes = [
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
  { path: 'dashboard', loadComponent: () => import('./modules/dashboard/dashboard.component').then(m => m.DashboardComponent) },
  { path: 'cultures', loadComponent: () => import('./modules/cultures/cultures.component').then(m => m.CulturesComponent) },
  { path: 'parcelles', loadComponent: () => import('./modules/parcelles/parcelles.component').then(m => m.ParcellesComponent) },
  { path: 'taches', loadComponent: () => import('./modules/taches/taches.component').then(m => m.TachesComponent) },
  {path: 'rendements', loadComponent: () => import('./modules/rendement/rendement.component').then(m => m.RendementComponent) },
];
