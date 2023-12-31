import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import * as z from 'zod'

import { Button } from '@/components/ui/button'
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Link } from 'react-router-dom'
import { AppDispatch } from '@redux/store'
import { useDispatch } from 'react-redux'
import { register } from '@redux/slices/user.slice'
import { toastMessage } from '@lib/utils'

const formSchema = z
  .object({
    email: z.string().email({
      message: 'Please enter a valid email.',
    }),
    password: z
      .string()
      .refine(
        value => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/.test(value),
        {
          message:
            'Password must contain at least 1 uppercase, 1 lowercase, and 1 number and must be at least 8 characters and at most 32 characters.',
        },
      ),
    confirmPassword: z
      .string()
      .refine(
        value => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/.test(value),
        {
          message:
            'Password must contain at least 1 uppercase, 1 lowercase, and 1 number and must be at least 8 characters and at most 32 characters.',
        },
      ),
    displayName: z
      .string()
      .min(3, {
        message: 'Display name must be at least 3 characters',
      })
      .max(24, {
        message: 'Display name must be at most 24 characters',
      }),
  })
  .refine(data => data.password === data.confirmPassword, {
    message: 'Passwords do not match.',
    path: ['confirmPassword'],
  })

export function Register() {
  const dispatch = useDispatch<AppDispatch>()
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
      confirmPassword: '',
      displayName: '',
    },
  })
  const renderConfirmPasswordMessage = () => {
    if (!form.formState.errors.confirmPassword) return null
    if (
      form.formState.errors.confirmPassword.message ===
      'Passwords do not match.'
    ) {
      return (
        <div className="text-[0.8rem] font-medium text-destructive">
          {form.formState.errors.confirmPassword.message}
        </div>
      )
    }
    return (
      <div className="text-[0.8rem] font-medium text-destructive">
        Password must contain:
        <ul className="list-disc list-inside">
          <li>At least 1 uppercase</li>
          <li>At least 1 lowercase</li>
          <li>At least 1 number</li>
          <li>At least 8 characters</li>
          <li>At most 32 characters</li>
        </ul>
      </div>
    )
  }

  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    const result = await dispatch(register(values))
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toastMessage(`Welcome, ${result.payload.displayName}!`, 'default')
    } catch (err: any) {
      toastMessage(err.message, 'destructive')
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen">
      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(onSubmit)}
          className="w-4/12 p-8 space-y-4 border rounded-lg shadow-lg"
        >
          <div className="text-3xl text-center text-foreground">Hi there!</div>
          <div className="text-center text-muted-foreground">
            Create your account to access all the lingering memories.
          </div>
          <FormField
            control={form.control}
            name="email"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                  <Input placeholder="Email" {...field} />
                </FormControl>
                <FormDescription>We'll never share your email.</FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="displayName"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Display Name</FormLabel>
                <FormControl>
                  <Input placeholder="Display Name" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="password"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                  <Input type="password" placeholder="Password" {...field} />
                </FormControl>
                {form.formState.errors.password && (
                  <div className="text-[0.8rem] font-medium text-destructive">
                    Password must contain:
                    <ul className="list-disc list-inside">
                      <li>At least 1 uppercase</li>
                      <li>At least 1 lowercase</li>
                      <li>At least 1 number</li>
                      <li>At least 8 characters</li>
                      <li>At most 32 characters</li>
                    </ul>
                  </div>
                )}
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="confirmPassword"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Confirm Password</FormLabel>
                <FormControl>
                  <Input
                    type="password"
                    placeholder="Confirm password"
                    {...field}
                  />
                </FormControl>
                {renderConfirmPasswordMessage()}
              </FormItem>
            )}
          />

          <p>
            Already have an account?{' '}
            <Link to="/login" className="text-primary hover:underline">
              Login
            </Link>
          </p>
          <Button
            className={
              form.formState.isSubmitting
                ? 'bg-muted cursor-loading hover:bg-muted'
                : 'bg-primary hover:bg-opacity-60 text-white'
            }
            type="submit"
          >
            {form.formState.isSubmitting ? 'Loading...' : 'Register'}
          </Button>
        </form>
      </Form>
    </div>
  )
}
