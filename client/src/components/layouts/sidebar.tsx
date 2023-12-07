import { buttonVariants } from '@/components/ui/button'
import { Icon } from '@iconify/react'
import { cn } from '@lib/utils'
import { Link, useLocation } from 'react-router-dom'
import { AppDispatch, useAppSelector } from '@redux/store'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { useEffect } from 'react'
import { getCategories } from '@redux/slices/category.slice'
import { useDispatch } from 'react-redux'

type SidebarItem = {
  id?: string
  title: string
  icon: string
  href: string
  url?: string
}
const sidebarItems: SidebarItem[] = [
  {
    title: 'Photos',
    icon: 'solar:library-bold-duotone',
    href: '/photos',
  },
  //   {
  //     title: 'Memories',
  //     icon: 'ri:memories-line',
  //     href: '/memories',
  //   },
  //   {
  //     title: 'Favorites',
  //     icon: 'material-symbols:favorite',
  //     href: '/favorites',
  //   },
]
let categoryItems: SidebarItem[] = []

export default function Sidebar({ className }: { className?: string }) {
  const location = useLocation()
  const { pathname } = location
  const user = useAppSelector(state => state.user).user
  const categories = useAppSelector(state => state.category).categories
  categoryItems = categories.map(category => {
    return {
      title: category.name,
      icon: 'ri:folder-line',
      href: `/category/${category.id}`,
      url: category.url,
    }
  })
  const dispatch = useDispatch<AppDispatch>()
  useEffect(() => {
    dispatch(getCategories())
  }, [])

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
                  ? 'text-primary hover:bg-muted hover:text-primary font-bold'
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
                <div className="hidden lg:flex"> {user.displayName}</div>
              </div>
            </Link>
            <div className="flex items-center justify-between gap-4 px-4 py-2">
              {' '}
              <div className="hidden text-xl md:flex">Library</div>
            </div>

            {sidebarItems.map(item => (
              <Link
                key={item.href}
                to={item.href}
                className={cn(
                  buttonVariants({ variant: 'ghost' }),
                  pathname === item.href
                    ? 'text-primary hover:bg-muted hover:text-primary font-bold'
                    : 'hover:bg-muted hover:text-primary',
                  'justify-start w-full text-left h-12',
                )}
              >
                <div className="flex items-center justify-between gap-4">
                  {' '}
                  <Icon icon={item.icon} className="w-8 h-8 mr-2" />
                  <div className="hidden lg:flex">{item.title}</div>
                </div>
              </Link>
            ))}
          </div>
          <Link
            key={'/category'}
            to={'/category'}
            className={cn(
              buttonVariants({ variant: 'ghost' }),
              pathname === '/category'
                ? 'text-primary hover:bg-muted hover:text-primary font-bold'
                : 'hover:bg-muted hover:text-primary',
              'justify-start w-full text-left h-12',
            )}
          >
            <div className="flex items-center justify-between gap-4">
              {' '}
              <Icon
                icon="material-symbols-light:category"
                className="block w-8 h-8 mr-2 md:hidden"
              />
              <div className="hidden text-xl md:flex">Category</div>
            </div>
          </Link>
          <div className="py-2">
            {categoryItems.map(item => (
              <Link
                key={item.href}
                to={item.href}
                className={cn(
                  buttonVariants({ variant: 'ghost' }),
                  pathname === item.href
                    ? 'text-primary hover:bg-muted hover:text-primary font-bold'
                    : 'hover:bg-muted hover:text-primary',
                  'justify-start w-full text-left h-12',
                )}
              >
                <div className="flex items-center justify-between gap-4">
                  {' '}
                  <img
                    className="w-8 h-8 mr-2 rounded-full"
                    src={item.url}
                    alt=""
                  />
                  <div className="hidden lg:flex">{item.title}</div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
