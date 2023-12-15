import { useParams } from 'react-router'
import { motion } from 'framer-motion'
import {
  useCategory,
  useCategoryPhotos,
  useRemovePhotoFromCategory,
} from '@/hooks/category.hook'
import PhotoImage from '@/components/photo-image'
import { useAppSelector } from '@/redux/store'
import UpdateCategoryNameForm from '@components/form/update-category-name-form'
import UpdateCategoryMemoForm from '@components/form/update-category-memo-form'
import AddPhotosDialog from '@components/dialog/add-photos-dialog'
import {
  ContextMenu,
  ContextMenuContent,
  ContextMenuItem,
  ContextMenuTrigger,
} from '@/components/ui/context-menu'

export default function Category() {
  const { categoryId } = useParams()
  const { data: category } = useCategory(categoryId)
  const { data: photos } = useCategoryPhotos(categoryId)

  const { mutateAsync: removePhotoFromCategory } = useRemovePhotoFromCategory(
    categoryId as string,
  )
  const favorite = useAppSelector(state => state.category.favoriteCategory)
  const renderPage = () => {
    if (!photos || !category) return <div>no data</div>
    const renderForm = () => {
      if (category.name === 'favorite')
        return <div className="text-3xl text-bold text-primary">Favorite</div>
      return (
        <>
          <UpdateCategoryNameForm category={category} />
          <UpdateCategoryMemoForm category={category} />
        </>
      )
    }
    return (
      <div className="flex flex-col h-screen pt-20">
        <div className="flex flex-col justify-center gap-4 p-8">
          {renderForm()}
          <AddPhotosDialog
            categoryId={category.id}
            categoryTitle={category.name}
          />
          <div className="font-semi text-muted-foreground">
            Create at {new Date(category.createdAt).toDateString()}
          </div>
          <div className="font-semi text-muted-foreground">
            Last update at {new Date(category.updatedAt).toDateString()}
          </div>
          <div className="grid grid-cols-3 gap-2 md:grid-cols-4 lg:grid-cols-6">
            {photos.map(photo => {
              return (
                <ContextMenu key={photo.id}>
                  <ContextMenuTrigger>
                    <PhotoImage photo={photo} favoriteId={favorite.id} />
                  </ContextMenuTrigger>
                  <ContextMenuContent>
                    <ContextMenuItem
                      className="focus:text-destructive"
                      onClick={() => {
                        removePhotoFromCategory(photo.id)
                      }}
                    >
                      Delete photo from category
                    </ContextMenuItem>
                  </ContextMenuContent>
                </ContextMenu>
              )
            })}
          </div>
        </div>
      </div>
    )
  }
  return (
    <motion.div
      key={categoryId}
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
    >
      {renderPage()}
    </motion.div>
  )
}
