import { Photo } from '@redux/types/response.type'

export const sortPhotosByMonthAndYear = (photos: Photo[]) => {
  const photosByYearAndMonth = new Map<string, Photo[]>()   
  photos.forEach(photo => {
    const date = new Date(photo.takenAt)
    const year = date.getFullYear()
    const month = date.getMonth()
    const key = new Date(year, month).toDateString()
    if (!photosByYearAndMonth.has(key)) {
      photosByYearAndMonth.set(key, [photo])
    } else {
      const photos = photosByYearAndMonth.get(key)
      photos?.push(photo)
      photosByYearAndMonth.set(key, photos!)
    }
  })
  return Array.from(photosByYearAndMonth).sort((a, b) => {
    const dateA = new Date(a[0])
    const dateB = new Date(b[0])
    return dateB.getTime() - dateA.getTime()
  })
}
export const convertToMonth = (month: number) => {
  const months = [
    'January',
    'Febuary',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'Octobor',
    'November',
    'December',
  ]
  return months[month]
}
