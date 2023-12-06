import { buttonVariants } from '@/components/ui/button'
import { Icon } from '@iconify/react'
import { cn } from '@lib/utils'
import { Link, useLocation } from 'react-router-dom'
import { useAppSelector } from '@redux/store'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

type SidebarItem = {
  title: string
  icon: string
  href: string
}
const sidebarItems: SidebarItem[] = [
  {
    title: 'Library',
    icon: 'solar:library-bold-duotone',
    href: '/photos',
  },
  {
    title: 'Memories',
    icon: 'ri:memories-line',
    href: '/memories',
  },
  {
    title: 'Favorites',
    icon: 'material-symbols:favorite',
    href: '/favorites',
  },
]
export default function Sidebar({ className }: { className?: string }) {
  const location = useLocation()
  const { pathname } = location
  const user = useAppSelector(state => state.user).user

  return (
    <div className={cn('bg-white', className)}>
      <div className="pt-20 space-y-4">
        <div className="px-3 py-2">
          <div className="space-y-1">
            <Link
              key="/profile"
              to="/profile"
              className={cn(
                buttonVariants({ variant: 'ghost' }),
                pathname === '/profile'
                  ? 'text-primary hover:bg-muted hover:text-primary'
                  : 'hover:bg-muted hover:text-primary',
                'justify-start w-full text-left h-12',
              )}
            >
              <div className="flex items-center justify-between gap-4">
                {' '}
                <Avatar className="w-8 h-8">
                  <AvatarImage src={user.avatar} />
                  <AvatarFallback>CN</AvatarFallback>
                </Avatar>
                {user.displayName}
              </div>
            </Link>

            <h2 className="px-4 mb-2 text-xl font-bold text-black">Photos</h2>
            {sidebarItems.map(item => (
              <Link
                key={item.href}
                to={item.href}
                className={cn(
                  buttonVariants({ variant: 'ghost' }),
                  pathname === item.href
                    ? 'text-primary hover:bg-muted hover:text-primary bg-muted'
                    : 'hover:bg-muted hover:text-primary',
                  'justify-start w-full text-left h-12',
                )}
              >
                <div className="flex items-center justify-between gap-4">
                  {' '}
                  <Icon icon={item.icon} className="w-8 h-8 mr-2" />
                  {item.title}
                </div>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
