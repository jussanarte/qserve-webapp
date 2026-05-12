import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { TranslateModule } from '@ngx-translate/core';
import { AuthService } from '../../../../core/services/auth.service';
import { environment } from '../../../../../environments/environment';

@Component({
  selector: 'app-reports',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './reports.component.html',
  styleUrls: ['./reports.component.scss']
})
export class ReportsComponent {
  form: FormGroup;
  loading = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private auth: AuthService
  ) {
    const today = new Date().toISOString().slice(0, 10);
    this.form = this.fb.group({
      date_from: [today, Validators.required],
      date_to:   [today, Validators.required],
    });
  }

  export(format: 'csv' | 'pdf'): void {
    if (this.form.invalid) return;
    this.loading = true;

    const { date_from, date_to } = this.form.value;
    const url = `${environment.apiUrl}/reports/tickets?format=${format}&date_from=${date_from}&date_to=${date_to}`;

    this.http.get(url, { responseType: 'blob' }).subscribe({
      next: (blob) => {
        const ext  = format === 'pdf' ? 'pdf' : 'csv';
        const link = document.createElement('a');
        link.href     = URL.createObjectURL(blob);
        link.download = `qserve-report-${date_from}-${date_to}.${ext}`;
        link.click();
        URL.revokeObjectURL(link.href);
        this.loading = false;
      },
      error: () => { alert('Erro ao exportar'); this.loading = false; }
    });
  }
}