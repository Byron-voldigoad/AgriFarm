import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AssistanceIAComponent } from './assistance-ia.component';
import { RecommandationService } from './recommandation.service';

@NgModule({
  declarations: [
    AssistanceIAComponent
  ],
  imports: [
    CommonModule
  ],
  providers: [
    RecommandationService
  ],
  exports: [
    AssistanceIAComponent
  ]
})
export class AssistanceIAModule { }
