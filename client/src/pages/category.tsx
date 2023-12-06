import { Button } from '@components/ui/button'
import { Separator } from '@components/ui/separator'
import { useAppSelector } from '@redux/store'
import { motion } from 'framer-motion'
import { Link } from 'react-router-dom'

export default function Category() {
  const categories = useAppSelector(state => state.category).categories
  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="pt-20"
    >
      <div className="flex items-center justify-between px-8 pb-4">
        <p className="text-xl text-primary">Category</p>
        <div>
          <Button className="text-accent" variant={'ghost'} size="lg">
            Create category
          </Button>
        </div>
      </div>
      <Separator />
      <div className="flex flex-wrap justify-start gap-4 px-8 mt-8">
        {categories.map(category => (
          <Link
            key={category.id}
            to={`/category/${category.id}`}
            className="transition-all duration-200 ease-in-out hover:scale-105"
          >
            <div
              key={category.id}
              className="flex flex-col items-center justify-center gap-2 h-72"
            >
              <img
                src={category.url}
                alt={category.name}
                className="h-64 rounded-lg"
              />
              <div className="text-black">{category.name}</div>
              <div className="text-black">{category.numPhotos} photos</div>
            </div>
          </Link>
        ))}
      </div>
    </motion.div>
  )
}
