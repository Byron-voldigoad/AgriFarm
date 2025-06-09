import { Injectable } from '@angular/core';
import {
  Storage,
  ref,
  uploadBytes,
  getDownloadURL,
} from '@angular/fire/storage';

@Injectable({ providedIn: 'root' })
export class FirebaseStorageService {
  constructor(private storage: Storage) {}

  async uploadUserPhoto(file: File, userId: string): Promise<string> {
    const filePath = `users/${userId}/${file.name}`;
    const storageRef = ref(this.storage, filePath);
    await uploadBytes(storageRef, file);
    return getDownloadURL(storageRef);
  }
}
