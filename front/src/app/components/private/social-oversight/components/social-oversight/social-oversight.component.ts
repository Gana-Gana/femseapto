import { Component, OnInit } from '@angular/core';
import { FileUploadService } from '../../../../../services/file-upload.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-social-oversight',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './social-oversight.component.html',
  styleUrl: './social-oversight.component.css',
})
export class SocialOversightComponent {
  files: string[] = [];

  constructor(private fileUploadService: FileUploadService) {}

  ngOnInit() {
    this.getFiles();
  }

  getFiles() {
    this.fileUploadService.getFiles().subscribe(
      (data: string[]) => {
        this.files = data;
      },
      (error: any) => {
        console.error('Error getting files:', error);
      }
    );
  }

  getDownloadUrl(fileName: string): string {
    return this.fileUploadService.downloadFile(fileName);
  }
}
