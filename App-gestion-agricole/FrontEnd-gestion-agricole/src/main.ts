// main.ts
import { bootstrapApplication } from '@angular/platform-browser';
import { appConfig } from './app/app.config'; // Importez la config
import { AppComponent } from './app/app.component';

bootstrapApplication(AppComponent, appConfig) // Utilisez appConfig ici
  .catch(err => console.error(err));