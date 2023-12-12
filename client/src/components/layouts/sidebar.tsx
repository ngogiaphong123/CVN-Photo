import { buttonVariants } from '@/components/ui/button'
import { Icon } from '@iconify/react'
import { cn } from '@lib/utils'
import { Link, useLocation } from 'react-router-dom'
import { AppDispatch, useAppSelector } from '@redux/store'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { useEffect } from 'react'
import { getCategories } from '@redux/slices/category.slice'
import { useDispatch } from 'react-redux'
import CategorySidebar from '@components/layouts/category-sidebar'

export type SidebarItem = {
  id?: string
  title: string
  icon: string
  href: string
  url?: string
}
const sidebarItems: SidebarItem[] = [
  {
    title: 'Photos',
    icon: 'material-symbols-light:photo-library-rounded',
    href: '/photos',
  },
]
let categoryItems: SidebarItem[] = []

export default function Sidebar({ className }: { className?: string }) {
  const location = useLocation()
  const { pathname } = location
  const user = useAppSelector(state => state.user).user
  const categories = useAppSelector(state => state.category).categories.filter(
    category => {
      if (category.name === 'favorite') {
        if (sidebarItems.find(item => item.title === 'Favorite')) return false
        sidebarItems.push({
          title: 'Favorite',
          icon: 'material-symbols-light:favorite-rounded',
          href: `/category/${category.id}`,
          url: category.url,
        })
        return false
      }
      return category.name !== 'favorite' && category.name !== 'uncategorized'
    },
  )
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
                'justify-start truncate w-[95%] text-left h-12',
              )}
            >
              <div className="flex items-center justify-between gap-4 ">
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
                  'justify-start truncate w-[95%] text-left h-12',
                )}
              >
                <div className="flex items-center justify-between gap-4">
                  {' '}
                  <Icon icon={item.icon} className="w-8 h-8 mr-2" />
                  <p className="hidden lg:inline-block">{item.title}</p>
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
              'justify-start truncate w-[95%] text-left h-12',
            )}
          >
            <div className="flex items-center justify-between gap-4">
              {' '}
              <Icon
                icon="material-symbols-light:category"
                className="block w-8 h-8 mr-2 md:hidden"
              />
              <div className="hidden text-xl truncate md:flex">Category</div>
            </div>
          </Link>
          <div className="flex flex-col w-full py-2">
            {categoryItems.map(item => (
              <CategorySidebar key={item.href} item={item} />
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
