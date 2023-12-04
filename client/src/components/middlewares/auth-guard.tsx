import { useEffect, useState } from 'react'
import { AppDispatch } from '@redux/store'
import { useDispatch } from 'react-redux'
import { getMe } from '@redux/slices/user.slice'
import { Outlet } from 'react-router-dom'
import Loading from '@components/layouts/loading'

export function AuthGuard() {
  const [isLoading, setIsLoading] = useState<boolean>(true)
  const dispatch = useDispatch<AppDispatch>()
  const render = () => {
    if (isLoading) return <Loading />
    return <Outlet />
  }
  useEffect(() => {
    async function checkAuth() {
      await dispatch(getMe())
      setIsLoading(false)
    }
    checkAuth()
  }, [])
  return <>{render()}</>
}
