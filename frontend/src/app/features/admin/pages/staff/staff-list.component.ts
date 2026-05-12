import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { User, StaffFormData } from '../../../../core/models/user.model';
import { UserService } from '../../../../core/services/user.service';

@Component({
  selector: 'app-staff-list',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './staff-list.component.html',
  styleUrls: ['./staff-list.component.scss'],
})
export class StaffListComponent implements OnInit {
  attendants: User[] = [];
  loading = true;
  saving = false;
  showModal = false;
  editingUser: User | null = null;
  error = '';
  form: FormGroup;

  constructor(private users: UserService, private fb: FormBuilder) {
    this.form = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(3)]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.minLength(6)]],
      language: ['pt', Validators.required],
      is_active: [true],
    });
  }

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading = true;
    this.users.attendants().subscribe({
      next: attendants => {
        this.attendants = attendants;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      },
    });
  }

  openCreate(): void {
    this.editingUser = null;
    this.form.reset({ name: '', email: '', password: '', language: 'pt', is_active: true });
    this.form.get('password')?.setValidators([Validators.required, Validators.minLength(6)]);
    this.form.get('password')?.updateValueAndValidity();
    this.error = '';
    this.showModal = true;
  }

  openEdit(user: User): void {
    this.editingUser = user;
    this.form.reset({
      name: user.name,
      email: user.email,
      password: '',
      language: user.language,
      is_active: user.is_active,
    });
    this.form.get('password')?.setValidators([Validators.minLength(6)]);
    this.form.get('password')?.updateValueAndValidity();
    this.error = '';
    this.showModal = true;
  }

  save(): void {
    if (this.form.invalid) return;

    this.saving = true;
    const payload = this.form.value as StaffFormData;
    if (!payload.password) {
      delete payload.password;
    }

    const request = this.editingUser
      ? this.users.updateAttendant(this.editingUser.id, payload)
      : this.users.createAttendant(payload);

    request.subscribe({
      next: () => {
        this.showModal = false;
        this.saving = false;
        this.load();
      },
      error: err => {
        this.error = err.error?.message ?? 'Erro';
        this.saving = false;
      },
    });
  }

  remove(user: User): void {
    if (!confirm(`${user.name}?`)) return;
    this.users.deleteAttendant(user.id).subscribe({
      next: () => this.load(),
      error: err => alert(err.error?.message ?? 'Erro'),
    });
  }
}
