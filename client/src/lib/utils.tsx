import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import Cookies from 'universal-cookie'
import { Icon } from '@iconify/react'
import { Photo } from '@redux/types/response.type'
import { toast } from '@components/ui/use-toast'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export const setCookie = (name: string, value: string) => {
  const cookies = new Cookies()
  cookies.set(name, value, { path: '/' })
}

export const unsetCookie = (name: string) => {
  const cookies = new Cookies()
  cookies.remove(name)
}

export const getCookie = (name: string) => {
  const cookies = new Cookies()
  return cookies.get(name)
}

export function isEmpty(obj: any) {
  for (const prop in obj) {
    if (Object.hasOwn(obj, prop)) {
      return false
    }
  }

  return true
}

export const sortPhotosByMonthAndYear = (photos: Photo[] | undefined) => {
  if (!photos) return []
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
    'October',
    'November',
    'December',
  ]
  return months[month]
}

export const renderPhotoDetailIcon = (
  name: string,
  isFavorite: boolean = false,
) => {
  return (
    <div className="flex items-center justify-center w-12 h-12 rounded-full hover:opacity-80">
      <Icon
        icon={`material-symbols-light:${name}`}
        className={`w-8 h-8 ${isFavorite ? 'text-red-600' : 'text-white'}`}
      />
    </div>
  )
}

export const toastMessage = (
  message: string,
  variant: 'default' | 'destructive' | null | undefined,
) => {
  const title = variant === 'destructive' ? 'Oops!' : undefined
  toast({
    title,
    description: `${message}`,
    variant: variant || 'default',
  })
}
