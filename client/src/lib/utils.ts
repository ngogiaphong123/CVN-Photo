import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import Cookies from 'universal-cookie'

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
