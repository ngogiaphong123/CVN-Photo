import {
  ContextMenu,
  ContextMenuContent,
  ContextMenuItem,
  ContextMenuTrigger,
} from '@/components/ui/context-menu'
import { useDeleteCategory } from '@/hooks/category.hook'
import { SidebarItem } from '@components/layouts/sidebar'
import { cn } from '@/lib/utils'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { buttonVariants } from '@components/ui/button'

export default function CategorySidebar({ item }: { item: SidebarItem }) {
  const location = useLocation()
  const { pathname } = location
  const id = item.href.split('/')[2]
  const navigate = useNavigate()
  const { mutateAsync: deleteCategory } = useDeleteCategory(id)
  return (
    <>
      {' '}
      <ContextMenu>
        <ContextMenuTrigger>
          {' '}
          <Link
            key={item.href}
            to={item.href}
            className={cn(
              buttonVariants({ variant: 'ghost' }),
              pathname === item.href
                ? 'text-primary hover:bg-muted hover:text-primary font-bold'
                : 'hover:bg-muted hover:text-primary',
              'justify-start line-clamp-1 text-left h-12 w-[95%]',
            )}
          >
            <div className="flex items-center justify-between gap-4">
              {' '}
              <img
                className="w-8 h-8 mr-2 rounded-full"
                src={item.url}
                alt=""
              />
              <p className="hidden lg:inline-block">{item.title}</p>
            </div>
          </Link>
        </ContextMenuTrigger>
        <ContextMenuContent>
          <ContextMenuItem
            className="focus:text-destructive"
            onClick={() => {
              deleteCategory()
              if(pathname === item.href) navigate('/category')
            }}
          >
            Delete category
          </ContextMenuItem>
        </ContextMenuContent>
      </ContextMenu>
    </>
  )
}
