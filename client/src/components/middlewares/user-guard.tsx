import { useAppSelector } from '@redux/store'
import { Navigate, Outlet } from 'react-router-dom'
import { isEmpty } from '@lib/utils'

export function UserGuard() {
  const user = useAppSelector(state => state.user).user
  const render = () => {
    if (isEmpty(user)) return <Navigate to="/login" />
    return <Outlet />
  }
  return <>{render()}</>
}
