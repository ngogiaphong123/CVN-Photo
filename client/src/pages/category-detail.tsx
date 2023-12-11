import { useParams } from 'react-router'
import PhotosSkeleton from '@components/layouts/photos-skeleton'
import { motion } from 'framer-motion'
import { Skeleton } from '@components/ui/skeleton'
import { useCategory, useCategoryPhotos } from '@/hooks/category.hook'
import PhotoImage from '@/components/photo-image'
import { useAppSelector } from '@/redux/store'
import UpdateCategoryNameForm from '@components/form/update-category-name-form'
import UpdateCategoryMemoForm from '@components/form/update-category-memo-form'
export default function CategoryDetail() {
  const { categoryId } = useParams()
  const { data: category } = useCategory(categoryId)
  const { data: photos, isLoading, isError } = useCategoryPhotos(categoryId)
  const favorite = useAppSelector(state => state.category.favoriteCategory)
  const renderPage = () => {
    if (isError) return <div>error</div>
    if (isLoading && !photos) {
      return (
        <div className="flex flex-col h-screen pt-20">
          <div className="flex flex-col justify-center gap-4 p-4">
            <Skeleton className="h-10 w-100" />
            <Skeleton className="h-10 w-100" />
            <PhotosSkeleton />
          </div>
        </div>
      )
    }
    if (!photos || !category) return <div>no data</div>
    return (
      <div className="flex flex-col h-screen pt-20">
        <div className="flex flex-col justify-center gap-4 p-8">
          <UpdateCategoryNameForm category={category} />
          <UpdateCategoryMemoForm category={category} />
          <div className="font-semi text-muted-foreground">
            Create at {new Date(category.createdAt).toDateString()}
          </div>
          <div className="font-semi text-muted-foreground">
            Last update at {new Date(category.updatedAt).toDateString()}
          </div>
          <div className="grid grid-cols-3 gap-2 md:grid-cols-4 lg:grid-cols-6">
            {photos.map(photo => (
              <PhotoImage
                photo={photo}
                favoriteId={favorite.id}
                key={photo.id}
              />
            ))}
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
