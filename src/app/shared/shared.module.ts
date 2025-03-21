import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CommonTableComponent } from './components/common-table/common-table.component';

@NgModule({
  declarations: [CommonTableComponent],
  imports: [CommonModule],
  exports: [CommonTableComponent] // Exportamos para que pueda usarse en otros módulos
})
export class SharedModule {}
