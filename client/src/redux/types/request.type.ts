export type LoginInput = {
  email: string
  password: string
}

export type RegisterInput = {
  email: string
  password: string
  displayName: string
}

export type UpdateProfileInput = {
  displayName: string
}

export type UpdatePhotoInput = {
  name?: string
  description?: string
  takenAt?: string
}
