import { Component, Input, OnChanges, ElementRef, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import QRCode from 'qrcode';

@Component({
  selector: 'app-ticket-qr',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './ticket-qr.component.html',
  styleUrls: ['./ticket-qr.component.scss'],
})
export class TicketQrComponent implements OnChanges {
  @Input() ticketNumber = '';
  @Input() queueName = '';
  @ViewChild('canvas', { static: true }) canvas!: ElementRef<HTMLCanvasElement>;

  ngOnChanges(): void {
    if (!this.ticketNumber) return;
    const data = JSON.stringify({
      ticket: this.ticketNumber,
      queue: this.queueName,
      ts: Date.now(),
    });
    QRCode.toCanvas(this.canvas.nativeElement, data, {
      width: 180,
      color: { dark: '#030027', light: '#F2F3D9' },
    });
  }
}
