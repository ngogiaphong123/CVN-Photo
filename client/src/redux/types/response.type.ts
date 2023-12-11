export type User = {
  id: string
  email: string
  avatar: string
  displayName: string
  createdAt: string
  updatedAt: string
}

export type Photo = {
  id: string
  name: string
  description: string
  url: string
  publicId: string
  size: string
  userId: string
  takenAt: string
  createdAt: string
  updatedAt: string
  next?: string
  previous?: string
  isFavorite: number
}

export type Category = {
  id: string
  name: string
  memo: string
  url: string
  publicId: string
  createdAt: string
  updatedAt: string
  numPhotos: number
}
